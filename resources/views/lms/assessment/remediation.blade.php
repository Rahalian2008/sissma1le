@extends('layouts.app')

@section('title', 'Program Remedial & Pengayaan - LMS SMAN 1 Lengkong')

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

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Penjaminan Mutu & Ketuntasan Belajar • SMAN 1 Lengkong
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Program Remedial & Pengayaan
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Pengelolaan bimbingan remedial bagi siswa yang belum mencapai Kriteria Ketercapaian TP serta penyediaan materi pengayaan bagi siswa dengan capaian melampaui rata-rata.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($isTeacher || $isKurikulum)
                    <button onclick="openModal('modalCreateRemedial')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Rencanakan Program</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('lms.assessment.remediation') }}" class="flex items-center gap-3">
            <select name="course_class_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Pilih Kelas Pembelajaran --</option>
                @foreach($myClasses as $cls)
                    <option value="{{ $cls->id }}" {{ $classFilter == $cls->id ? 'selected' : '' }}>
                        {{ $cls->subject->name ?? '-' }} ({{ $cls->schoolClass->name ?? '-' }})
                    </option>
                @endforeach
            </select>
        </form>

        <span class="text-xs text-slate-500 font-semibold">
            Menampilkan {{ $plans->total() }} program
        </span>
    </div>

    <!-- Daftar Program Remedial & Pengayaan -->
    @if($plans->isEmpty())
        <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
            <span class="text-4xl">🌱</span>
            <h3 class="text-base font-bold text-slate-800 dark:text-white mt-3">Belum Ada Program Remedial & Pengayaan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                Rencanakan bimbingan khusus perbaikan nilai atau tugas pengayaan terstruktur bagi peserta didik.
            </p>
            <button onclick="openModal('modalCreateRemedial')" class="mt-4 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                + Rencanakan Program Sekarang
            </button>
        </div>
    @else
        <div class="space-y-4">
            @foreach($plans as $plan)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-3 hover:border-indigo-400 dark:hover:border-indigo-600 transition">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider
                                {{ $plan->type === 'REMEDIAL' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300' }}">
                                {{ $plan->type }}
                            </span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                {{ $plan->courseClass->subject->name ?? 'Mapel' }} ({{ $plan->courseClass->schoolClass->name ?? 'Kelas' }})
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($plan->status === 'COMPLETED')
                                <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200">
                                    ✓ Tuntas Dilaksanakan
                                </span>
                            @else
                                <form method="POST" action="{{ route('lms.assessment.remediation.complete', $plan) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs">
                                        Tandai Tuntas
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $plan->topic }}</h3>
                        @if($plan->objective)
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">
                                [TP {{ $plan->objective->tp_code }}] {{ $plan->objective->title }}
                            </p>
                        @endif
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                        <div><strong>Metode Pelaksanaan:</strong> {{ $plan->remedial_method }}</div>
                        <div class="mt-1"><strong>Rencana Aktivitas:</strong> {{ $plan->description }}</div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span>Peserta: <strong>{{ is_array($plan->student_ids) ? count($plan->student_ids) : 0 }} Siswa</strong></span>
                        <span>Jadwal: <strong>{{ $plan->scheduled_date?->translatedFormat('l, d F Y') ?? '-' }}</strong></span>
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                {{ $plans->links() }}
            </div>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL: BUAT PROGRAM REMEDIAL / PENGAYAAN  -->
<!-- ========================================== -->
<div id="modalCreateRemedial" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto my-8">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Rencanakan Program Remedial / Pengayaan</h3>
                <p class="text-xs text-slate-500">Pendampingan ketuntasan Tujuan Pembelajaran siswa.</p>
            </div>
            <button onclick="closeModal('modalCreateRemedial')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.assessment.remediation.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas Pembelajaran *</label>
                <select name="course_class_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                    <option value="">-- Pilih Kelas Pembelajaran --</option>
                    @foreach($myClasses as $cls)
                        <option value="{{ $cls->id }}" {{ $classFilter == $cls->id ? 'selected' : '' }}>
                            {{ $cls->subject->name ?? '-' }} ({{ $cls->schoolClass->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Program *</label>
                    <select name="type" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="REMEDIAL">Remedial (Perbaikan)</option>
                        <option value="PENGAYAAN">Pengayaan (Pendalaman)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pelaksanaan *</label>
                    <input type="date" name="scheduled_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan Pembelajaran (TP) yang Diperbaiki</label>
                <select name="learning_objective_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                    <option value="">-- Pilih TP Terkait --</option>
                    @foreach($objectives as $tp)
                        <option value="{{ $tp->id }}">[TP {{ $tp->tp_code }}] {{ $tp->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pokok Bahasan / Topik *</label>
                <input type="text" name="topic" required placeholder="Contoh: Remedial Pemahaman Percabangan If-Else" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Metode Remedial</label>
                <input type="text" name="remedial_method" value="Penugasan mandiri terstruktur dan tes butir soal setara" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <!-- Pilih Siswa Peserta -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Peserta Didik *</label>
                <div class="max-h-36 overflow-y-auto p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 space-y-1 text-xs">
                    @forelse($availableStudents as $std)
                        <label class="flex items-center gap-2 hover:bg-slate-100 dark:hover:bg-slate-800 p-1 rounded-lg cursor-pointer">
                            <input type="checkbox" name="student_ids[]" value="{{ $std->id }}" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $std->name }}</span>
                            <span class="text-slate-400">({{ $std->nisn ?? 'NISN -' }})</span>
                        </label>
                    @empty
                        <p class="text-[11px] text-slate-400 italic">Pilih kelas di filter atas terlebih dahulu untuk menampilkan daftar siswa kelas.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreateRemedial')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    Simpan Rencana Program
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
</script>
@endsection

