@extends('layouts.app')

@section('title', 'Supervisi Akademik & Kurikulum - SMAN 1 Lengkong')

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
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 border border-blue-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Modul H • Penjaminan Mutu & Monitoring Pimpinan
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Supervisi Akademik & Kurikulum
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Audit komprehensif kelengkapan 5 dimensi perangkat ajar guru (Modul Ajar, Jurnal KBM, Bank Soal TP, Refleksi, Remedial) dan instrumen observasi supervisi kelas Kepala Sekolah / Pengawas.
                </p>
            </div>

            @if($isLeader)
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="openModal('modalCreateSupervision')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Catat Hasil Supervisi Guru</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Quick Stats Matrix -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-slate-400 text-xs font-semibold">Total Guru Terdata</span>
                <span class="text-xl sm:text-2xl font-black text-white">{{ $stats['total_teachers'] }} Orang</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-blue-400 text-xs font-semibold">Sesi Supervisi</span>
                <span class="text-xl sm:text-2xl font-black text-blue-300">{{ $stats['supervision_count'] }} Kali</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-emerald-400 text-xs font-semibold">Rata-rata Skor Sekolah</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-300">{{ $stats['average_score'] }} / 100</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-amber-400 text-xs font-semibold">Predikat Baik & Amat Baik</span>
                <span class="text-xl sm:text-2xl font-black text-amber-300">{{ $stats['high_performing'] }} Sesi</span>
            </div>
        </div>
    </div>

    <!-- Section 1: Matriks Audit Kesiapan 5 Dimensi Perangkat Guru -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📊</span>
                    <span>Matriks Kesiapan Administrasi & 5 Dimensi Kurikulum Merdeka</span>
                </h3>
                <p class="text-xs text-slate-500">Pemantauan kelengkapan Modul Ajar, Jurnal KBM, Bank Soal TP, Refleksi Pembelajaran, dan Tindak Lanjut Remedial.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                Tahun Ajaran 2026/2027
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">
                        <th class="py-3 px-3">Nama Pendidik</th>
                        <th class="py-3 px-3 text-center">1. Modul Ajar</th>
                        <th class="py-3 px-3 text-center">2. Jurnal KBM</th>
                        <th class="py-3 px-3 text-center">3. Bank Soal TP</th>
                        <th class="py-3 px-3 text-center">4. Refleksi</th>
                        <th class="py-3 px-3 text-center">5. Remedial</th>
                        <th class="py-3 px-3 text-center">Kesiapan Administrasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($auditMatrix as $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $item['teacher']->name }}</div>
                                <div class="text-[11px] text-slate-500">NIP: {{ $item['teacher']->nip ?: '-' }} • {{ $item['total_classes'] }} Kelas Binaan</div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item['modul_ajar']['approved'] > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800' }}">
                                    {{ $item['modul_ajar']['approved'] }} Disahkan ({{ $item['modul_ajar']['total'] }})
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item['journal']['verified'] > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800' }}">
                                    {{ $item['journal']['verified'] }} Terverifikasi ({{ $item['journal']['total'] }})
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item['questions']['with_tp'] > 0 ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800' }}">
                                    {{ $item['questions']['with_tp'] }} Soal TP ({{ $item['questions']['total'] }})
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item['reflections'] > 0 ? 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800' }}">
                                    {{ $item['reflections'] }} Refleksi
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item['remediation'] > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800' }}">
                                    {{ $item['remediation'] }} Program
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <div class="w-32 mx-auto">
                                    <div class="flex items-center justify-between text-[11px] font-extrabold mb-1">
                                        <span class="text-slate-700 dark:text-slate-300">{{ $item['readiness_percent'] }}%</span>
                                        <span class="text-slate-400">Target 100%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                        <div class="h-2 rounded-full transition-all duration-500 {{ $item['readiness_percent'] >= 80 ? 'bg-emerald-500' : ($item['readiness_percent'] >= 50 ? 'bg-indigo-500' : 'bg-amber-500') }}" style="width: {{ $item['readiness_percent'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">Tidak ada data pendidik.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Riwayat Sesi Supervisi Akademik & Observasi Kelas -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>🛡️</span>
                    <span>Riwayat Instrumen & Sesi Observasi Supervisi Akademik</span>
                </h3>
                <p class="text-xs text-slate-500">Hasil penilaian 4 pilar instrumen (Perencanaan, KBM Kelas, Asesmen, Refleksi) oleh Pimpinan Sekolah.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">Pendidik Terobservasi</th>
                        <th class="py-3 px-3">Supervisor</th>
                        <th class="py-3 px-2 text-center">Perencanaan</th>
                        <th class="py-3 px-2 text-center">Pelaksanaan</th>
                        <th class="py-3 px-2 text-center">Asesmen</th>
                        <th class="py-3 px-2 text-center">Refleksi</th>
                        <th class="py-3 px-3 text-center">Skor Akhir & Predikat</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($supervisions as $s)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $s->supervision_date ? $s->supervision_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $s->teacher->name ?? 'Guru' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $s->courseClass->subject->name ?? 'Semua Mapel' }} ({{ $s->courseClass->schoolClass->name ?? '-' }})</div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $s->supervisor->name ?? 'Supervisor' }}</div>
                                <div class="text-[10px] text-slate-400">Pimpinan / Pengawas</div>
                            </td>
                            <td class="py-3 px-2 text-center font-mono">{{ $s->planning_score }}</td>
                            <td class="py-3 px-2 text-center font-mono">{{ $s->execution_score }}</td>
                            <td class="py-3 px-2 text-center font-mono">{{ $s->assessment_score }}</td>
                            <td class="py-3 px-2 text-center font-mono">{{ $s->reflection_score }}</td>
                            <td class="py-3 px-3 text-center">
                                <div class="font-extrabold text-sm text-slate-900 dark:text-white font-mono">{{ $s->final_score }}</div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $s->predicate === 'AMAT BAIK' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : ($s->predicate === 'BAIK' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $s->predicate }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('lms.supervision.print', $s) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/50 transition" title="Cetak Lembar Supervisi">
                                        🖨️
                                    </a>
                                    @if($isLeader)
                                        <form method="POST" action="{{ route('lms.supervision.destroy', $s) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan supervisi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">
                                <span class="text-3xl block mb-2">🛡️</span>
                                Belum ada instrumen supervisi yang dicatat pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $supervisions->links() }}
        </div>
    </div>
</div>

<!-- Modal Catat Hasil Supervisi Baru -->
@if($isLeader)
<div id="modalCreateSupervision" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🛡️</span>
                <span>Instrumen Penilaian Supervisi Akademik Guru</span>
            </h3>
            <button onclick="closeModal('modalCreateSupervision')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.supervision.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Guru yang Disupervisi *</label>
                    <select name="teacher_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} (NIP: {{ $t->nip ?: '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Observasi *</label>
                    <input type="date" name="supervision_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas & Mata Pelajaran Teramati (Opsional)</label>
                <select name="course_class_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                    <option value="">-- Pengawasan Umum / Seluruh Kelas Ampu --</option>
                    @foreach($courseClasses as $cc)
                        <option value="{{ $cc->id }}">{{ $cc->subject->name ?? 'Mapel' }} — {{ $cc->schoolClass->name ?? 'Kelas' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Skor Perencanaan (0-100) *</label>
                    <input type="number" step="0.5" min="0" max="100" name="planning_score" value="85" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center font-bold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Skor KBM Kelas (0-100) *</label>
                    <input type="number" step="0.5" min="0" max="100" name="execution_score" value="86" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center font-bold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Skor Asesmen (0-100) *</label>
                    <input type="number" step="0.5" min="0" max="100" name="assessment_score" value="84" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center font-bold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Skor Refleksi (0-100) *</label>
                    <input type="number" step="0.5" min="0" max="100" name="reflection_score" value="85" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center font-bold text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Hasil Pengamatan Supervisor</label>
                <textarea name="notes" rows="2" placeholder="Sintaks KBM 3 tahap terlaksana dengan baik, partisipasi peserta didik interaktif..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rekomendasi Tindak Lanjut Pimpinan</label>
                <textarea name="recommendation" rows="2" placeholder="Pertahankan inovasi media pembelajaran dan lanjutkan program pembinaan tutor sebaya..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreateSupervision')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition">
                    Sahkan & Simpan Hasil Supervisi
                </button>
            </div>
        </form>
    </div>
</div>
@endif

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

