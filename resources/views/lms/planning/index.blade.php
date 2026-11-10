@extends('layouts.app')

@section('title', 'Pusat Perencanaan Pembelajaran & Modul Ajar - LMS SMAN 1 Lengkong')

@section('content')
<div class="space-y-6">
    @include('lms.partials.nav')

    <!-- Flash Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm space-y-1 shadow-xs">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Terdapat kendala saat menyimpan data:</span>
            </div>
            <ul class="list-disc pl-8 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Standar Kurikulum Merdeka • SMAN 1 Lengkong
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Perencanaan & Perangkat Ajar Digital
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Pengelolaan terpadu Capaian Pembelajaran (CP), Tujuan Pembelajaran (TP), Alur (ATP), dan Modul Ajar/RPP dengan bantuan asisten AI serta alur verifikasi resmi Wakasek Kurikulum.
                </p>
            </div>

            <!-- Action Buttons Header -->
            <div class="flex flex-wrap items-center gap-3">
                @if($isTeacher || $isKurikulum)
                    <button onclick="openModal('modalCreateLessonPlan')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Susun Modul Ajar</span>
                    </button>
                    <button onclick="openModal('modalAiObjective')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600/80 hover:bg-purple-600 text-white font-bold text-xs sm:text-sm shadow-md transition border border-purple-400/30">
                        <span>✨</span>
                        <span>AI Asisten Kurikulum</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Quick Stats Matrix Modul Ajar -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Total Modul Ajar</div>
                <div class="text-xl sm:text-2xl font-black text-white mt-1">{{ $planStats['total'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Draf Penyusunan</div>
                <div class="text-xl sm:text-2xl font-black text-slate-300 mt-1">{{ $planStats['draft'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-amber-600/40">
                <div class="text-[11px] font-bold text-amber-400 uppercase">Diajukan ke Kurikulum</div>
                <div class="text-xl sm:text-2xl font-black text-amber-300 mt-1">{{ $planStats['submitted'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-rose-600/40">
                <div class="text-[11px] font-bold text-rose-400 uppercase">Perlu Revisi</div>
                <div class="text-xl sm:text-2xl font-black text-rose-300 mt-1">{{ $planStats['revised'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-emerald-600/40">
                <div class="text-[11px] font-bold text-emerald-400 uppercase">Disetujui / KBM</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-300 mt-1">{{ $planStats['approved'] }}</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
        <a href="{{ route('lms.planning.index', ['tab' => 'lesson_plans']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition {{ $activeTab === 'lesson_plans' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:text-indigo-600 border border-slate-200 dark:border-slate-800' }}">
            <span>📖</span>
            <span>Modul Ajar / RPP Digital ({{ $planStats['total'] }})</span>
        </a>
        <a href="{{ route('lms.planning.index', ['tab' => 'objectives']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition {{ $activeTab === 'objectives' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:text-indigo-600 border border-slate-200 dark:border-slate-800' }}">
            <span>🎯</span>
            <span>Tujuan Pembelajaran (TP) ({{ $objectives->count() }})</span>
        </a>
        <a href="{{ route('lms.planning.index', ['tab' => 'outcomes']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition {{ $activeTab === 'outcomes' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:text-indigo-600 border border-slate-200 dark:border-slate-800' }}">
            <span>📋</span>
            <span>Capaian Pembelajaran (CP) ({{ $outcomes->count() }})</span>
        </a>
        <a href="{{ route('lms.planning.index', ['tab' => 'flows']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition {{ $activeTab === 'flows' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:text-indigo-600 border border-slate-200 dark:border-slate-800' }}">
            <span>🗺️</span>
            <span>Alur Tujuan (ATP)</span>
        </a>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: MODUL AJAR / RPP DIGITAL           -->
    <!-- ========================================== -->
    @if($activeTab === 'lesson_plans')
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftar Modul Ajar Digital</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Modul ajar per pertemuan KBM, lengkap dengan langkah pembelajaran dan keterkaitan TP.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openModal('modalCreateLessonPlan')" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <span>+</span>
                        <span>Tambah Modul Ajar</span>
                    </button>
                </div>
            </div>

            @if($lessonPlans->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <span class="text-4xl">📄</span>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white mt-3">Belum Ada Modul Ajar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                        Mulai susun modul ajar digital dengan menentukan pertemuan, keterkaitan Tujuan Pembelajaran (TP), serta sintaks pembelajaran Kurikulum Merdeka.
                    </p>
                    <button onclick="openModal('modalCreateLessonPlan')" class="mt-4 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                        + Susun Modul Ajar Sekarang
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($lessonPlans as $plan)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs flex flex-col justify-between hover:border-indigo-400 dark:hover:border-indigo-600 transition">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider
                                        {{ $plan->status === 'APPROVED' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800' : '' }}
                                        {{ $plan->status === 'SUBMITTED' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-300 dark:border-amber-800' : '' }}
                                        {{ $plan->status === 'REVISED' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-800' : '' }}
                                        {{ $plan->status === 'DRAFT' ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700' : '' }}">
                                        {{ $plan->status === 'APPROVED' ? 'Disetujui' : ($plan->status === 'SUBMITTED' ? 'Diajukan' : ($plan->status === 'REVISED' ? 'Revisi' : 'Draf')) }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                        Pertemuan ke-{{ $plan->meeting_number }} • {{ $plan->time_allocation_minutes }}m
                                    </span>
                                </div>

                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2">
                                        {{ $plan->title }}
                                    </h3>
                                    <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mt-0.5">
                                        {{ $plan->courseClass->subject->name ?? 'Mata Pelajaran' }} • {{ $plan->courseClass->schoolClass->name ?? '' }} (Fase {{ $plan->grade_level === 'X' ? 'E' : 'F' }})
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                        Guru: {{ $plan->teacher->user->name ?? $plan->teacher->name ?? 'Guru Pengampu' }}
                                    </p>
                                </div>

                                <!-- Keterkaitan TP -->
                                @if($plan->objectives->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($plan->objectives as $tp)
                                            <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-semibold border border-indigo-200 dark:border-indigo-800/50">
                                                TP {{ $tp->tp_code }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Catatan Review Wakasek jika ada -->
                                @if($plan->review_notes)
                                    <div class="p-2.5 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 text-[11px] text-amber-800 dark:text-amber-300">
                                        <div class="font-bold flex items-center gap-1 mb-0.5">
                                            <span>💬 Catatan Kurikulum:</span>
                                        </div>
                                        <p class="line-clamp-2 italic">{{ $plan->review_notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Toolbar -->
                            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                                <a href="{{ route('lms.planning.lesson_plans.print', $plan) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    <span>Cetak</span>
                                </a>

                                <div class="flex items-center gap-1.5">
                                    <!-- Jika Guru & Draf/Revisi: Ajukan ke Wakasek -->
                                    @if(in_array($plan->status, ['DRAFT', 'REVISED']))
                                        <form method="POST" action="{{ route('lms.planning.lesson_plans.submit', $plan) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Ajukan modul ajar ini untuk ditinjau oleh Wakasek Kurikulum?')" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition flex items-center gap-1 shadow-xs">
                                                <span>🚀</span>
                                                <span>Ajukan</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Jika Wakasek Kurikulum / Admin: Tombol Review -->
                                    @if($isKurikulum)
                                        <button onclick="openReviewModal('{{ $plan->id }}', '{{ addslashes($plan->title) }}')" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition flex items-center gap-1 shadow-xs">
                                            <span>✍️</span>
                                            <span>Review</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $lessonPlans->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: TUJUAN PEMBELAJARAN (TP)           -->
    <!-- ========================================== -->
    @if($activeTab === 'objectives')
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Bank Tujuan Pembelajaran (TP)</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Rumusan kompetensi dan lingkup materi operasional untuk mata pelajaran.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="openModal('modalCreateObjective')" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <span>+</span>
                        <span>Tambah TP Manual</span>
                    </button>
                    <button onclick="openModal('modalAiObjective')" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <span>✨</span>
                        <span>AI Asisten TP</span>
                    </button>
                </div>
            </div>

            @if($objectives->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <span class="text-4xl">🎯</span>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white mt-3">Belum Ada Tujuan Pembelajaran</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                        Gunakan tombol Tambah TP Manual atau gunakan AI Asisten Kurikulum untuk membuat draf rumusan TP secara otomatis.
                    </p>
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-4 py-3">Kode TP</th>
                                    <th class="px-4 py-3">Mata Pelajaran</th>
                                    <th class="px-4 py-3">Rumusan Tujuan Pembelajaran</th>
                                    <th class="px-4 py-3">Kompetensi & Lingkup Materi</th>
                                    <th class="px-4 py-3">Alokasi & Semester</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($objectives as $tp)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                        <td class="px-4 py-3 font-black text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                            {{ $tp->tp_code }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $tp->subject->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-700 dark:text-slate-300 max-w-md">
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $tp->title }}</div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{ $tp->description }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                            <div><span class="font-semibold text-slate-700 dark:text-slate-300">Kompetensi:</span> {{ $tp->competency ?? '-' }}</div>
                                            <div class="mt-0.5"><span class="font-semibold text-slate-700 dark:text-slate-300">Materi:</span> {{ $tp->scope_material ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[10px]">
                                                {{ $tp->hours_allocation }} JP • Sem {{ $tp->target_semester }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 3: CAPAIAN PEMBELAJARAN (CP)          -->
    <!-- ========================================== -->
    @if($activeTab === 'outcomes')
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Capaian Pembelajaran (CP) Resmi</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Keputusan BSKAP Kemendikbudristek No. 032/H/KR/2024 untuk Fase E (Kelas X) & Fase F (Kelas XI - XII).</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openModal('modalCreateOutcome')" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <span>+</span>
                        <span>Tambah Capaian (CP)</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($outcomes as $outcome)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 text-xs font-black border border-indigo-200 dark:border-indigo-800">
                                Fase {{ $outcome->phase }} (Kelas {{ $outcome->grade_level }})
                            </span>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                {{ $outcome->code ?? 'CP-STD' }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $outcome->subject->name ?? 'Mata Pelajaran' }}
                            </h3>
                            @if($outcome->element)
                                <p class="text-xs text-cyan-600 dark:text-cyan-400 font-semibold mt-0.5">
                                    Elemen: {{ $outcome->element }}
                                </p>
                            @endif
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs text-slate-600 dark:text-slate-300 leading-relaxed border border-slate-100 dark:border-slate-800">
                            {{ $outcome->description }}
                        </div>

                        <div class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span>Status: Aktif</span>
                            <span>{{ $outcome->objectives()->count() }} TP Terkait</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                        <span class="text-4xl">📋</span>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white mt-3">Belum Ada Capaian Pembelajaran</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                            Data CP dapat diisi manual atau di-seed dari data Kemendikbudristek BSKAP 032/H/2024.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 4: ALUR TUJUAN PEMBELAJARAN (ATP)      -->
    <!-- ========================================== -->
    @if($activeTab === 'flows')
        <div class="space-y-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Alur Tujuan Pembelajaran (ATP)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pengurutan hierarki Tujuan Pembelajaran dari tingkat dasar hingga tingkat lanjut dalam 1 Fase.</p>
            </div>

            @if($flows->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                    <span class="text-4xl">🗺️</span>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white mt-3">Belum Ada Dokumen ATP</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                        Alur Tujuan Pembelajaran disusun untuk memetakan urutan mengajar guru per semester selama satu tahun ajaran.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($flows as $flow)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 text-xs font-black">
                                    Fase {{ $flow->phase }} • Kelas {{ $flow->grade_level }}
                                </span>
                                <span class="text-xs font-bold text-slate-500">{{ $flow->total_hours }} JP</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $flow->title }}</h3>
                            <p class="text-xs text-slate-500">{{ $flow->subject->name ?? '-' }} • {{ $flow->teacher->name ?? 'Tim MGMP' }}</p>
                            <p class="text-xs text-slate-600 dark:text-slate-400">{{ $flow->flow_rationale }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL: SUSUN MODUL AJAR DIGITAL           -->
<!-- ========================================== -->
<div id="modalCreateLessonPlan" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-3xl w-full p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto my-8">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Susun Modul Ajar Kurikulum Merdeka</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Rancang KBM interaktif per pertemuan sesuai regulasi Permendikdasmen.</p>
            </div>
            <button onclick="closeModal('modalCreateLessonPlan')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.planning.lesson_plans.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas & Mata Pelajaran *</label>
                    <select name="course_class_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="">-- Pilih Kelas Pembelajaran --</option>
                        @foreach($myClasses as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->subject->name ?? '' }} - {{ $cls->schoolClass->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas & Semester *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="grade_level" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                            <option value="X">Kelas X (Fase E)</option>
                            <option value="XI">Kelas XI (Fase F)</option>
                            <option value="XII">Kelas XII (Fase F)</option>
                        </select>
                        <select name="semester" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                            <option value="GANJIL">Ganjil</option>
                            <option value="GENAP">Genap</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Topik Modul Ajar *</label>
                    <input type="text" name="title" id="planTitle" required placeholder="Contoh: Berpikir Komputasional dan Pemrograman Dasar" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pertemuan & Alokasi *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="meeting_number" value="1" min="1" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white" title="Pertemuan Ke-">
                        <input type="number" name="time_allocation_minutes" value="90" min="30" max="240" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white" title="Alokasi (Menit)">
                    </div>
                </div>
            </div>

            <!-- Hubungkan dengan Tujuan Pembelajaran (TP) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Tujuan Pembelajaran (TP) yang Disasar:</label>
                <div class="max-h-28 overflow-y-auto p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 space-y-1.5 text-xs">
                    @forelse($objectives as $tp)
                        <label class="flex items-center gap-2 hover:bg-slate-100 dark:hover:bg-slate-800 p-1 rounded-lg cursor-pointer">
                            <input type="checkbox" name="objective_ids[]" value="{{ $tp->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">[{{ $tp->tp_code }}]</span>
                            <span class="text-slate-700 dark:text-slate-300">{{ $tp->title }} ({{ $tp->subject->name ?? '' }})</span>
                        </label>
                    @empty
                        <p class="text-[11px] text-slate-400 italic">Belum ada Tujuan Pembelajaran yang tersimpan. Modul ajar tetap dapat dibuat.</p>
                    @endforelse
                </div>
            </div>

            <!-- Model & Profil Pelajar Pancasila -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Model Pembelajaran</label>
                    <select name="learning_model" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="Problem-Based Learning (PBL)">Problem-Based Learning (PBL)</option>
                        <option value="Project-Based Learning (PjBL)">Project-Based Learning (PjBL)</option>
                        <option value="Discovery / Inquiry Learning">Discovery / Inquiry Learning</option>
                        <option value="Direct Instruction & Praktik">Direct Instruction & Praktik</option>
                        <option value="Cooperative Learning">Cooperative Learning</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Dimensi Profil Pancasila</label>
                    <div class="flex flex-wrap gap-2 text-[11px]">
                        @foreach(['Bernalar Kritis', 'Kreatif', 'Gotong Royong', 'Mandiri'] as $dim)
                            <label class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="target_profile[]" value="{{ $dim }}" checked class="rounded text-indigo-600">
                                <span>{{ $dim }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Langkah KBM (3 Bagian Terstruktur) -->
            <div class="space-y-3 pt-2 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">Skenario Langkah Pembelajaran</h4>
                    <button type="button" onclick="triggerAiPlanDraft()" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1">
                        <span>✨ Draf Cepat dengan AI</span>
                    </button>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">1. Pendahuluan & Apersepsi (15 Menit) *</label>
                    <textarea name="step_introduction" id="stepIntro" rows="2" required placeholder="Guru membuka salam, doa, apersepsi keterkaitan materi, penyampaian tujuan pembelajaran..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">2. Kegiatan Inti (60 Menit) *</label>
                    <textarea name="step_core" id="stepCore" rows="3" required placeholder="Sintaks model pembelajaran: orientasi masalah, kerja kelompok/individu, penalaran, pembuatan produk..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">3. Penutup & Refleksi (15 Menit) *</label>
                    <textarea name="step_closing" id="stepClosing" rows="2" required placeholder="Peserta didik menyimpulkan materi, refleksi perasaan belajar, tindak lanjut pertemuan depan..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>
            </div>

            <!-- Rencana Asesmen & LKPD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200 dark:border-slate-800">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">Rencana Asesmen Formatif</label>
                    <textarea name="assessment_formative" id="stepFormative" rows="2" placeholder="Observasi keaktifan diskusi, rubrik performa presentasi..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">Lembar Kerja Peserta Didik (LKPD Ringkas)</label>
                    <textarea name="lkpd_content" id="stepLkpd" rows="2" placeholder="Pertanyaan pemantik atau studi kasus panduan diskusi siswa..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreateLessonPlan')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    Simpan Draf Modul Ajar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: TAMBAH TUJUAN PEMBELAJARAN (TP)    -->
<!-- ========================================== -->
<div id="modalCreateObjective" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Tambah Tujuan Pembelajaran (TP)</h3>
                <p class="text-xs text-slate-500">Rumusan capaian operasional peserta didik.</p>
            </div>
            <button onclick="closeModal('modalCreateObjective')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.planning.objectives.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran *</label>
                <select name="subject_id" id="tpFormSubjectId" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                    @foreach($subjects as $sb)
                        <option value="{{ $sb->id }}">{{ $sb->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode TP *</label>
                    <input type="text" name="tp_code" id="tpFormCode" required placeholder="Misal: 10.1" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Semester & JP *</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <select name="target_semester" id="tpFormSemester" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-2 text-slate-900 dark:text-white">
                            <option value="GANJIL">Ganjil</option>
                            <option value="GENAP">Genap</option>
                        </select>
                        <input type="number" name="hours_allocation" id="tpFormHours" value="4" min="1" max="20" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-2 text-slate-900 dark:text-white" title="Alokasi JP">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Ringkasan TP *</label>
                <input type="text" name="title" id="tpFormTitle" required placeholder="Contoh: Menjelaskan prinsip dasar algoritma pencarian" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Lengkap TP *</label>
                <textarea name="description" id="tpFormDesc" rows="2" required placeholder="Peserta didik mampu memahami dan menerapkan..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kompetensi (Kata Kerja Operasional)</label>
                    <input type="text" name="competency" id="tpFormCompetency" placeholder="Memahami, Menganalisis" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lingkup Materi</label>
                    <input type="text" name="scope_material" id="tpFormScope" placeholder="Algoritma & Pemrograman" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreateObjective')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    Simpan TP
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: TAMBAH CAPAIAN PEMBELAJARAN (CP)   -->
<!-- ========================================== -->
<div id="modalCreateOutcome" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Tambah Capaian Pembelajaran (CP)</h3>
                <p class="text-xs text-slate-500">Standar Capaian Pembelajaran Fase E atau Fase F.</p>
            </div>
            <button onclick="closeModal('modalCreateOutcome')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.planning.outcomes.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran *</label>
                <select name="subject_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                    @foreach($subjects as $sb)
                        <option value="{{ $sb->id }}">{{ $sb->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Fase *</label>
                    <select name="phase" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="E">Fase E (Kelas X)</option>
                        <option value="F">Fase F (Kelas XI - XII)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas *</label>
                    <select name="grade_level" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode CP</label>
                    <input type="text" name="code" placeholder="CP-INF-E" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Elemen CP</label>
                    <input type="text" name="element" placeholder="Berpikir Komputasional" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Capaian Pembelajaran *</label>
                <textarea name="description" rows="3" required placeholder="Pada akhir fase ini, peserta didik mampu..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreateOutcome')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    Simpan CP
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: AI ASISTEN TUJUAN PEMBELAJARAN (TP)-->
<!-- ========================================== -->
<div id="modalAiObjective" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-purple-500/30 max-w-xl w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-2xl">✨</span>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">AI Asisten Kurikulum Merdeka</h3>
                    <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Generator Rumusan TP berbasis Taksonomi Bloom & BSKAP.</p>
                </div>
            </div>
            <button onclick="closeModal('modalAiObjective')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran</label>
                <input type="text" id="aiSubjectName" value="{{ $subjects->first()->name ?? 'Informatika' }}" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                    <select id="aiGrade" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="X">Kelas X (Fase E)</option>
                        <option value="XI">Kelas XI (Fase F)</option>
                        <option value="XII">Kelas XII (Fase F)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Lingkup Materi *</label>
                    <input type="text" id="aiTopic" placeholder="Misal: Jaringan Komputer dan Internet" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <button type="button" id="btnRunAiTp" onclick="runAiTpGenerator()" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                <span id="aiTpSpinner" class="hidden animate-spin">⏳</span>
                <span>✨ Buat Rumusan TP dengan AI</span>
            </button>

            <!-- Preview Hasil AI -->
            <div id="aiTpResultContainer" class="hidden space-y-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-purple-700 dark:text-purple-300">💡 Rekomendasi Rumusan AI:</span>
                    <button type="button" onclick="copyAiTpToForm()" class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] hover:bg-indigo-700">
                        📋 Salin ke Form Tambah TP
                    </button>
                </div>
                <div id="aiTpResultText" class="p-3 rounded-xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 text-xs text-slate-800 dark:text-slate-200 whitespace-pre-wrap max-h-48 overflow-y-auto leading-relaxed">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: REVIEW / PENGESAHAN MODUL AJAR     -->
<!-- ========================================== -->
<div id="modalReviewPlan" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Verifikasi Modul Ajar KBM</h3>
                <p id="reviewPlanTitle" class="text-xs text-indigo-600 font-bold truncate max-w-xs"></p>
            </div>
            <button onclick="closeModal('modalReviewPlan')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="reviewPlanForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keputusan Peninjauan *</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="p-3 rounded-xl border border-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 flex items-center gap-2 cursor-pointer font-bold text-xs">
                        <input type="radio" name="action" value="APPROVE" checked class="text-emerald-600">
                        <span>Setujui (KBM)</span>
                    </label>
                    <label class="p-3 rounded-xl border border-rose-300 bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 flex items-center gap-2 cursor-pointer font-bold text-xs">
                        <input type="radio" name="action" value="REVISE" class="text-rose-600">
                        <span>Minta Revisi</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Verifikator Kurikulum</label>
                <textarea name="review_notes" rows="3" placeholder="Masukkan arahan perbaikan atau catatan apresiasi untuk guru penyusun..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalReviewPlan')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    Kirim Keputusan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }

    function openReviewModal(planId, title) {
        const modal = document.getElementById('modalReviewPlan');
        const form = document.getElementById('reviewPlanForm');
        const titleEl = document.getElementById('reviewPlanTitle');
        if (modal && form) {
            form.action = `{{ url('/lms/planning/lesson-plans') }}/${planId}/review`;
            titleEl.textContent = title;
            modal.classList.remove('hidden');
        }
    }

    // AI Helper: TP Generator
    let lastAiTpData = null;
    async function runAiTpGenerator() {
        const topic = document.getElementById('aiTopic').value.trim();
        const subject = document.getElementById('aiSubjectName').value.trim();
        const grade = document.getElementById('aiGrade').value;

        if (!topic) {
            alert('Silakan ketikkan topik atau lingkup materi terlebih dahulu.');
            return;
        }

        const spinner = document.getElementById('aiTpSpinner');
        const btn = document.getElementById('btnRunAiTp');
        spinner.classList.remove('hidden');
        btn.disabled = true;

        try {
            const resp = await fetch("{{ route('lms.planning.objectives.ai_generate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    subject_name: subject,
                    topic: topic,
                    grade: grade
                })
            });

            const data = await resp.json();
            if (data.status === 'ok') {
                lastAiTpData = {
                    subject: subject,
                    topic: topic,
                    content: data.content
                };
                document.getElementById('aiTpResultText').textContent = data.content;
                document.getElementById('aiTpResultContainer').classList.remove('hidden');
            } else {
                alert('Gagal menghasilkan draf TP. Silakan coba kembali.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan koneksi saat memproses AI.');
        } finally {
            spinner.classList.add('hidden');
            btn.disabled = false;
        }
    }

    function copyAiTpToForm() {
        if (!lastAiTpData) return;
        closeModal('modalAiObjective');
        openModal('modalCreateObjective');

        const titleEl = document.getElementById('tpFormTitle');
        const descEl = document.getElementById('tpFormDesc');
        const scopeEl = document.getElementById('tpFormScope');

        if (titleEl) titleEl.value = `Menguasai materi ${lastAiTpData.topic}`;
        if (descEl) descEl.value = lastAiTpData.content;
        if (scopeEl) scopeEl.value = lastAiTpData.topic;
    }

    // AI Helper: Modul Ajar Step Assistant
    async function triggerAiPlanDraft() {
        const title = document.getElementById('planTitle').value.trim();
        if (!title) {
            alert('Silakan isi Judul / Topik Modul Ajar terlebih dahulu.');
            document.getElementById('planTitle').focus();
            return;
        }

        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = '⏳ Menghubungi AI...';
        btn.disabled = true;

        try {
            const resp = await fetch("{{ route('lms.planning.lesson_plans.ai_generate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    subject_name: 'Mata Pelajaran',
                    topic: title,
                    grade: 'X',
                    duration: 90
                })
            });

            const data = await resp.json();
            if (data.status === 'ok') {
                const intro = document.getElementById('stepIntro');
                const core = document.getElementById('stepCore');
                const closing = document.getElementById('stepClosing');
                const formative = document.getElementById('stepFormative');
                const lkpd = document.getElementById('stepLkpd');

                intro.value = `1. Guru memberi salam dan menyapa peserta didik.\n2. Melakukan apersepsi pemantik terkait ${title}.\n3. Menyampaikan tujuan pembelajaran dan cakupan kegiatan.`;
                core.value = data.content;
                closing.value = `1. Guru dan peserta didik membuat rangkuman dan kesimpulan pembelajaran.\n2. Melakukan refleksi pembelajaran bermakna.\n3. Menyampaikan tindak lanjut untuk pertemuan selanjutnya.`;
                formative.value = `Observasi keterlibatan diskusi kelompok dan penilaian keaktifan saat presentasi hasil diskusi.`;
                lkpd.value = `Diskusikan bersama kelompok: Analisislah permasalahan utama terkait "${title}" dan rumuskan solusi inovatif.`;
                alert('✨ Draf langkah pembelajaran AI berhasil disematkan!');
            }
        } catch (e) {
            console.error(e);
            alert('Kendala AI draf modul ajar.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>
@endsection

