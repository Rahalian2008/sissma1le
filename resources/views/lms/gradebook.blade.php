@extends('layouts.app')

@section('title', 'Buku Nilai: ' . $courseClass->subject->name . ' - ' . $courseClass->schoolClass->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'nilai']) }}" class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-indigo-600 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Buku Rekap Nilai Siswa (Gradebook)</h1>
                <p class="text-xs text-slate-500">{{ $courseClass->subject->name }} • Kelas {{ $courseClass->schoolClass->name }} • KKTP: {{ $courseClass->subject->kkm ?? 75 }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('lms.grades.export_excel', $courseClass->id) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs" title="Unduh Buku Nilai format Microsoft Excel (.xls)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Excel</span>
            </a>
            <a href="{{ route('lms.grades.print_pdf', $courseClass->id) }}" target="_blank" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs" title="Cetak / Simpan PDF Lembar Nilai Resmi dengan KOP dan TTD Kepala Sekolah">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF (KOP)</span>
            </a>
            <a href="{{ route('lms.grades.export', $courseClass->id) }}" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                <span>CSV</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center justify-between">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-800 dark:text-rose-300 font-bold flex items-center justify-between">
            <span>✕ {{ session('error') }}</span>
        </div>
    @endif

    <!-- FORM MANUAL: PENGATURAN PERSENTASE BOBOT PENILAIAN -->
    @php
        $wm = $courseClass->getWeightMaterial();
        $wa = $courseClass->getWeightAssignment();
        $wq = $courseClass->getWeightQuiz();
        $wt = $courseClass->getWeightAttendance();
        $canEditWeights = auth()->check() && auth()->user()->canEditLmsWeights($courseClass);
    @endphp

    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>⚙️ Pengaturan Bobot Persentase Penilaian Akhir</span>
                </h3>
                <p class="text-xs text-slate-500">Tentukan persentase kontribusi Materi, Tugas, Asesmen CBT, dan Presensi Kehadiran. Total harus tepat 100%.</p>
            </div>
            <div id="grade-weight-badge" class="px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700">
                Total Bobot: <span id="weight-total-display">100</span>% (Valid)
            </div>
        </div>

        @if($canEditWeights)
            <form action="{{ route('lms.grades.weights', $courseClass->id) }}" method="POST" onsubmit="return validateGradeWeights(event)" class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2 items-end">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📖 Materi Selesai (%)</label>
                    <input type="number" step="0.1" min="0" max="100" name="weight_material" id="w_mat" value="{{ $wm }}" oninput="recalcWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                    <span class="text-[10px] text-slate-400">Tuntas = 100, Rerata</span>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📝 Tugas & PR (%)</label>
                    <input type="number" step="0.1" min="0" max="100" name="weight_assignment" id="w_asg" value="{{ $wa }}" oninput="recalcWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                    <span class="text-[10px] text-slate-400">Rata-rata tugas</span>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">🎯 Asesmen CBT (%)</label>
                    <input type="number" step="0.1" min="0" max="100" name="weight_quiz" id="w_qiz" value="{{ $wq }}" oninput="recalcWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                    <span class="text-[10px] text-slate-400">Rata-rata ujian CBT</span>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📅 Presensi Mapel (%)</label>
                    <input type="number" step="0.1" min="0" max="100" name="weight_attendance" id="w_att" value="{{ $wt }}" oninput="recalcWeightTotal()" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                    <span class="text-[10px] text-slate-400">H=100, I/S=50, A=0</span>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <button type="submit" id="btn-save-weights" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        Simpan Bobot
                    </button>
                </div>
            </form>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <span class="text-slate-400 block text-[10px]">Materi Pelajaran</span>
                    <strong class="text-sm text-slate-900 dark:text-white">{{ $wm }}%</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <span class="text-slate-400 block text-[10px]">Tugas & PR</span>
                    <strong class="text-sm text-slate-900 dark:text-white">{{ $wa }}%</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <span class="text-slate-400 block text-[10px]">Asesmen & CBT</span>
                    <strong class="text-sm text-slate-900 dark:text-white">{{ $wq }}%</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <span class="text-slate-400 block text-[10px]">Presensi Mapel</span>
                    <strong class="text-sm text-slate-900 dark:text-white">{{ $wt }}%</strong>
                </div>
            </div>
        @endif
    </div>

    <!-- 4 Indikator Pemantauan Pembelajaran Aktif (Selalu Update Mengikuti Jumlah Konten) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span class="font-bold">Materi Pelajaran</span>
                <span class="p-1 rounded-lg bg-blue-500/10 text-blue-600">📖</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ $totalMaterialsCount ?? $materials->count() }}
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Bobot nilai {{ $wm }}%</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span class="font-bold">Tugas & PR</span>
                <span class="p-1 rounded-lg bg-indigo-500/10 text-indigo-600">📝</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ $totalAssignmentsCount ?? $assignments->count() }}
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Bobot nilai {{ $wa }}%</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span class="font-bold">Asesmen & CBT</span>
                <span class="p-1 rounded-lg bg-purple-500/10 text-purple-600">🎯</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ $totalQuizzesCount ?? $quizzes->count() }}
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Bobot nilai {{ $wq }}%</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span class="font-bold">Presensi Mapel</span>
                <span class="p-1 rounded-lg bg-emerald-500/10 text-emerald-600">📅</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ $totalAttendancesCount ?? $attendances->count() }}
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Bobot kehadiran {{ $wt }}%</p>
        </div>
    </div>

    <!-- Gradebook Matrix Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900 text-slate-200 uppercase font-extrabold tracking-wider">
                    <tr>
                        <th class="p-3.5 text-center">No</th>
                        <th class="p-3.5">NISN</th>
                        <th class="p-3.5">Nama Siswa</th>
                        <th class="p-3.5 text-center">Materi ({{ $wm }}%)</th>
                        <th class="p-3.5 text-center">Tugas & PR ({{ $wa }}%)</th>
                        <th class="p-3.5 text-center">Asesmen CBT ({{ $wq }}%)</th>
                        <th class="p-3.5 text-center">Presensi ({{ $wt }}%)</th>
                        <th class="p-3.5 text-center">Nilai Akhir</th>
                        <th class="p-3.5 text-center">Predikat</th>
                        <th class="p-3.5 text-center">Status KKTP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @php $no = 1; @endphp
                    @foreach($students as $st)
                        @php $g = $gradebook[$st->id] ?? null; @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-3.5 text-center text-slate-400 font-bold">{{ $no++ }}</td>
                            <td class="p-3.5 font-mono text-slate-500">{{ $st->nisn }}</td>
                            <td class="p-3.5 font-bold text-slate-900 dark:text-white">{{ $st->name }}</td>
                            <td class="p-3.5 text-center font-semibold text-slate-700 dark:text-slate-300">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $g['materials_completed'] ?? 0 }}/{{ $g['materials_count'] ?? $totalMaterialsCount }}</span>
                                <span class="block text-[10px] text-blue-600 dark:text-blue-400 font-bold">Rerata: {{ isset($g['material_avg']) && $g['material_avg'] !== null ? $g['material_avg'] : '-' }}</span>
                            </td>
                            <td class="p-3.5 text-center font-semibold text-slate-700 dark:text-slate-300">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $g['assignments_submitted'] ?? 0 }}/{{ $g['assignments_count'] ?? $totalAssignmentsCount }}</span>
                                <span class="block text-[10px] text-indigo-600 dark:text-indigo-400 font-bold">Rerata: {{ isset($g['assignment_avg']) && $g['assignment_avg'] !== null ? $g['assignment_avg'] : '-' }}</span>
                            </td>
                            <td class="p-3.5 text-center font-semibold text-slate-700 dark:text-slate-300">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $g['quizzes_completed'] ?? 0 }}/{{ $g['quizzes_count'] ?? $totalQuizzesCount }}</span>
                                <span class="block text-[10px] text-purple-600 dark:text-purple-400 font-bold">Rerata: {{ isset($g['quiz_avg']) && $g['quiz_avg'] !== null ? $g['quiz_avg'] : '-' }}</span>
                            </td>
                            <td class="p-3.5 text-center font-semibold text-slate-700 dark:text-slate-300">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $g['attendances_attended'] ?? 0 }}/{{ $g['attendances_count'] ?? $totalAttendancesCount }}</span>
                                <span class="block text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">Rerata: {{ isset($g['attendance_avg']) && $g['attendance_avg'] !== null ? $g['attendance_avg'] : '-' }}</span>
                            </td>
                            <td class="p-3.5 text-center font-black text-sm text-indigo-600 dark:text-indigo-400">{{ isset($g['final_score']) && $g['final_score'] !== null ? $g['final_score'] : '-' }}</td>
                            <td class="p-3.5 text-center font-bold">{{ $g['predicate'] ?? '-' }}</td>
                            <td class="p-3.5 text-center">
                                @if(isset($g['is_mastered']) && $g['is_mastered'] !== null)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $g['is_mastered'] ? 'bg-emerald-500/15 text-emerald-600 border border-emerald-500/20' : 'bg-rose-500/15 text-rose-600 border border-rose-500/20' }}">
                                        {{ $g['is_mastered'] ? 'TUNTAS' : 'REMEDIAL' }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function recalcWeightTotal() {
    const mat = parseFloat(document.getElementById('w_mat')?.value) || 0;
    const asg = parseFloat(document.getElementById('w_asg')?.value) || 0;
    const qiz = parseFloat(document.getElementById('w_qiz')?.value) || 0;
    const att = parseFloat(document.getElementById('w_att')?.value) || 0;
    const total = Math.round((mat + asg + qiz + att) * 10) / 10;
    
    const display = document.getElementById('weight-total-display');
    const badge = document.getElementById('grade-weight-badge');
    const btn = document.getElementById('btn-save-weights');
    
    if (display) display.textContent = total;
    
    if (Math.abs(total - 100) < 0.01) {
        if (badge) {
            badge.className = "px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700";
            badge.innerHTML = `Total Bobot: <span id="weight-total-display">${total}</span>% (Valid)`;
        }
        if (btn) btn.disabled = false;
    } else {
        if (badge) {
            badge.className = "px-3 py-1 rounded-xl text-xs font-black bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-700";
            badge.innerHTML = `Total Bobot: <span id="weight-total-display">${total}</span>% (Harus 100%)`;
        }
    }
}

function validateGradeWeights(e) {
    const mat = parseFloat(document.getElementById('w_mat')?.value) || 0;
    const asg = parseFloat(document.getElementById('w_asg')?.value) || 0;
    const qiz = parseFloat(document.getElementById('w_qiz')?.value) || 0;
    const att = parseFloat(document.getElementById('w_att')?.value) || 0;
    const total = Math.round((mat + asg + qiz + att) * 10) / 10;
    
    if (Math.abs(total - 100) > 0.01) {
        alert('Total persentase bobot harus pas 100%. Saat ini: ' + total + '%. Silakan sesuaikan.');
        e.preventDefault();
        return false;
    }
    return true;
}
</script>
@endsection
