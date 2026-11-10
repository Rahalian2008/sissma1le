@extends('layouts.app')

@section('title', 'Refleksi Pembelajaran Guru - SMAN 1 Lengkong')

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
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Modul F • Evaluasi Diri & Tindak Lanjut KBM
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Refleksi Pembelajaran Guru
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Evaluasi berkelanjutan efektivitas metode pengajaran, identifikasi kendala pemahaman siswa, dan perumusan rencana tindak lanjut KBM sesuai siklus Kurikulum Merdeka.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button onclick="openModal('modalCreateReflection')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tulis Refleksi Baru</span>
                </button>
                <button onclick="openModal('modalAiReflection')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-purple-600/30 transition">
                    <span>✨</span>
                    <span>Asisten AI Refleksi</span>
                </button>
                <a href="{{ route('lms.reflections.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs sm:text-sm shadow-md transition border border-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Rekap Refleksi</span>
                </a>
            </div>
        </div>

        <!-- Quick Stats Matrix -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-slate-400 text-xs font-semibold">Total Refleksi</span>
                <span class="text-xl sm:text-2xl font-black text-white">{{ $stats['total'] }}</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-emerald-400 text-xs font-semibold">Refleksi Selesai</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-300">{{ $stats['completed'] }}</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-indigo-400 text-xs font-semibold">Refleksi Bulan Ini</span>
                <span class="text-xl sm:text-2xl font-black text-indigo-300">{{ $stats['this_month'] }}</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-amber-400 text-xs font-semibold">Kelas Terevaluasi</span>
                <span class="text-xl sm:text-2xl font-black text-amber-300">{{ $stats['classes_covered'] }} Kelas</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
        <form method="GET" action="{{ route('lms.reflections.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Filter Kelas & Mata Pelajaran</label>
                <select name="course_class_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    <option value="">Semua Kelas & Mapel Binaan</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->subject->name ?? 'Mapel' }} — {{ $c->schoolClass->name ?? 'Kelas' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-48">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="SELESAI" {{ $selectedStatus === 'SELESAI' ? 'selected' : '' }}>SELESAI</option>
                    <option value="DRAFT" {{ $selectedStatus === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                </select>
            </div>

            @if($selectedClassId || $selectedStatus)
                <div class="self-end pb-0.5">
                    <a href="{{ route('lms.reflections.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                        Reset Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Daftar Kartu Refleksi -->
    <div class="space-y-4">
        @forelse($reflections as $reflection)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-xs transition hover:shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                            {{ $reflection->courseClass->subject->name ?? 'Mapel' }} • {{ $reflection->courseClass->schoolClass->name ?? 'Kelas' }}
                        </span>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            📅 {{ $reflection->reflection_date ? $reflection->reflection_date->format('d M Y') : '-' }}
                        </span>
                        <span class="text-xs text-slate-400">• Guru: {{ $reflection->teacher->name ?? '-' }}</span>
                        @if($reflection->lessonPlan)
                            <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                                Modul: {{ $reflection->lessonPlan->title }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $reflection->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' }}">
                            {{ $reflection->status }}
                        </span>

                        <form method="POST" action="{{ route('lms.reflections.destroy', $reflection) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan refleksi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                    <!-- Berjalan Efektif -->
                    <div class="bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 rounded-2xl p-4">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-800 dark:text-emerald-300 mb-1.5">
                            <span>✅</span>
                            <span>Aspek yang Berjalan Efektif</span>
                        </div>
                        <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $reflection->success_notes }}</p>
                    </div>

                    <!-- Kendala & Hambatan -->
                    <div class="bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-2xl p-4">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-rose-800 dark:text-rose-300 mb-1.5">
                            <span>⚠️</span>
                            <span>Kendala & Tantangan Belajar</span>
                        </div>
                        <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $reflection->challenges_notes }}</p>
                    </div>

                    <!-- Rencana Tindak Lanjut -->
                    <div class="bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 rounded-2xl p-4">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-800 dark:text-indigo-300 mb-1.5">
                            <span>🎯</span>
                            <span>Rencana Tindak Lanjut KBM</span>
                        </div>
                        <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $reflection->follow_up_action }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-800 rounded-3xl p-12 text-center">
                <span class="text-4xl block mb-2">💡</span>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum Ada Catatan Refleksi Pembelajaran</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-4">
                    Catatan evaluasi guru pasca pembelajaran membantu pemetaan ketuntasan kurikulum, perbaikan strategi mengajar, dan supervisi akademik.
                </p>
                <button onclick="openModal('modalCreateReflection')" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md hover:bg-indigo-500 transition">
                    + Mulai Tulis Refleksi
                </button>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $reflections->links() }}
        </div>
    </div>
</div>

<!-- Modal Tulis Refleksi Baru -->
<div id="modalCreateReflection" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <span>💡</span>
                <span>Tulis Catatan Refleksi KBM Guru</span>
            </h3>
            <button onclick="closeModal('modalCreateReflection')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.reflections.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas & Mata Pelajaran *</label>
                    <select name="course_class_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                        <option value="">Pilih Kelas Ampu</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                {{ $c->subject->name ?? 'Mapel' }} — {{ $c->schoolClass->name ?? 'Kelas' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Refleksi *</label>
                    <input type="date" name="reflection_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kaitkan Modul Ajar (Opsional)</label>
                <select name="lesson_plan_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                    <option value="">-- Tanpa Kaitan Modul Ajar Tertentu --</option>
                    @foreach($lessonPlans as $lp)
                        <option value="{{ $lp->id }}">Pertemuan {{ $lp->meeting_number }} : {{ $lp->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">1. Aspek yang Berhasil & Berjalan Efektif *</label>
                <textarea id="ref_success_notes" name="success_notes" rows="3" required placeholder="Contoh: Pembelajaran berbasis proyek berjalan interaktif, siswa aktif berdiskusi dalam kelompok dan presentasi lugas..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">2. Kendala / Tantangan Pemahaman Siswa di Kelas *</label>
                <textarea id="ref_challenges_notes" name="challenges_notes" rows="3" required placeholder="Contoh: Sekitar 4 siswa masih kesulitan memahami analisis data tabel dan membutuhkan bantuan tutor sebaya..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">3. Rencana Tindak Lanjut Perbaikan KBM Berikutnya *</label>
                <textarea id="ref_follow_up_action" name="follow_up_action" rows="3" required placeholder="Contoh: Menyiapkan lembar kerja panduan per langkah (scaffolding) dan sesi penguatan 15 menit awal..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="status" value="SELESAI" checked id="status_selesai" class="rounded text-indigo-600">
                    <label for="status_selesai" class="text-xs text-slate-600 dark:text-slate-400">Tandai Langsung Sebagai Selesai</label>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeModal('modalCreateReflection')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                        Simpan Refleksi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Asisten AI Refleksi -->
<div id="modalAiReflection" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <span>✨</span>
                <span>Asisten AI Refleksi Pembelajaran</span>
            </h3>
            <button onclick="closeModal('modalAiReflection')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            Asisten AI akan membantu merumuskan draf kalimat refleksi komprehensif (keberhasilan, tantangan, dan rencana perbaikan) berdasarkan topik pembelajaran.
        </p>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik KBM / Materi Pembelajaran *</label>
                <input type="text" id="ai_topic" placeholder="Contoh: Struktur Data dan Algoritma Pencarian" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                    <select id="ai_grade" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Kendala Utama</label>
                    <input type="text" id="ai_challenges" placeholder="Variasi konsentrasi / logika" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="pt-2">
                <button type="button" onclick="generateAiReflection()" id="btnGenerateAi" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>✨</span>
                    <span id="btnGenerateAiText">Hasilkan Draf Rekomendasi Refleksi</span>
                </button>
            </div>

            <!-- Area Hasil AI -->
            <div id="aiResultArea" class="hidden space-y-2 pt-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Hasil Rekomendasi AI:</label>
                <div id="aiResultText" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 whitespace-pre-line max-h-52 overflow-y-auto font-mono"></div>
                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" onclick="applyAiDraftToForm()" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition">
                        Gunakan Draf pada Formulir
                    </button>
                </div>
            </div>
        </div>
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

    let lastAiContent = '';

    function generateAiReflection() {
        const topic = document.getElementById('ai_topic').value;
        const grade = document.getElementById('ai_grade').value;
        const challenges = document.getElementById('ai_challenges').value;

        if (!topic) {
            alert('Silakan masukkan topik materi terlebih dahulu.');
            return;
        }

        const btn = document.getElementById('btnGenerateAi');
        const btnText = document.getElementById('btnGenerateAiText');
        btn.disabled = true;
        btnText.innerText = 'Menyusun draf refleksi...';

        fetch("{{ route('lms.reflections.ai_generate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: topic,
                grade: grade,
                challenges: challenges
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btnText.innerText = 'Hasilkan Draf Rekomendasi Refleksi';
            if (data.content) {
                lastAiContent = data.content;
                document.getElementById('aiResultArea').classList.remove('hidden');
                document.getElementById('aiResultText').innerText = data.content;
            } else {
                alert('Gagal menghasilkan refleksi AI.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerText = 'Hasilkan Draf Rekomendasi Refleksi';
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function applyAiDraftToForm() {
        closeModal('modalAiReflection');
        openModal('modalCreateReflection');
        
        // Coba pisahkan konten atau tempatkan di follow up / success notes
        document.getElementById('ref_success_notes').value = "Mayoritas peserta didik antusias dalam sesi eksplorasi dan diskusi kelompok.";
        document.getElementById('ref_challenges_notes').value = document.getElementById('ai_challenges').value || "Sebagian peserta didik masih membutuhkan bimbingan bertahap pada studi kasus analitis.";
        document.getElementById('ref_follow_up_action').value = lastAiContent || "Penguatan konsep terarah pada sesi awal pertemuan berikutnya.";
    }
</script>
@endsection

