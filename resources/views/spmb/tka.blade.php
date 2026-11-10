@extends('layouts.app')

@section('title', 'Jalur SPMB Nilai TKA (Tes Kemampuan Akademik)')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Jalur TKA -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-600 via-amber-700 to-orange-800 p-6 sm:p-8 text-white shadow-xl shadow-amber-500/10">
        <div class="absolute -right-8 -bottom-8 h-60 w-60 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-amber-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Jalur Khusus Seleksi Akademik Mandiri SMA</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Jalur SPMB Nilai TKA (Tes Kemampuan Akademik)</h1>
                <p class="text-amber-100 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed">
                    Sistem seleksi calon siswa baru {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} berbasis asesmen kompetensi: <strong>Literasi Bahasa</strong>, <strong>Numerasi / Penalaran Matematika</strong>, dan <strong>Logika Sains</strong> yang dikombinasikan dengan Nilai Rapor SMP.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('spmb.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition-all">
                    <span>&larr; Beranda SPMB</span>
                </a>

                <form action="{{ route('spmb.tka.calculate_rankings') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white text-amber-900 hover:bg-amber-50 font-black text-xs shadow-md transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Hitung Ulang Ranking</span>
                    </button>
                </form>

                <form action="{{ route('spmb.tka.auto_admit') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menetapkan ranking 1 s/d kuota sebagai Lolos Seleksi?')" class="inline">
                    @csrf
                    <input type="hidden" name="quota" value="{{ $quotaTka }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tetapkan Kelulusan Kuota TKA</span>
                    </button>
                </form>

                <a href="{{ route('spmb.print-recap', ['track' => 'TKA']) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-amber-950/40 hover:bg-amber-950/60 text-white font-bold text-xs border border-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Peringkat</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Formula & Aturan Pembobotan SPMB SMA -->
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-slate-900 dark:to-slate-800 border-2 border-amber-200 dark:border-amber-800/60 rounded-3xl p-5 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 dark:text-white text-sm">Formula Pembobotan Skor Seleksi TKA (SMA)</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                        <span class="font-mono font-bold text-amber-700 dark:text-amber-400">Skor Akhir Terbobot = (60% &times; Rata-rata Skor TKA) + (40% &times; Nilai Rata-rata Rapor SMP)</span>
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Skor TKA dihitung merata dari 3 domain uji: <strong>Literasi Bahasa</strong> (Indo/Inggris), <strong>Numerasi</strong> (Matematika & Penalaran Kuantitatif), dan <strong>Sains & Logika Ilmiah</strong>.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0 bg-white dark:bg-slate-800 px-4 py-2.5 rounded-2xl border border-amber-200 dark:border-slate-700 shadow-2xs">
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">Alokasi Kuota TKA</div>
                    <div class="text-base font-black text-amber-700 dark:text-amber-400">{{ $quotaTka }} Siswa</div>
                </div>
                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">Telah Diterima</div>
                    <div class="text-base font-black text-emerald-600 dark:text-emerald-400">{{ $admittedTkaCount }} Siswa</div>
                </div>
                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">Sisa Kuota</div>
                    <div class="text-base font-black text-blue-600 dark:text-blue-400">{{ max(0, $quotaTka - $admittedTkaCount) }} Kursi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Nilai Komponen TKA -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Rata-rata Literasi</span>
            <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ number_format($avgLiteracy, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Bahasa & Pemahaman Teks</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Rata-rata Numerasi</span>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($avgNumeracy, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Matematika & Penalaran</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Rata-rata Sains / Logika</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($avgScience, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Penalaran Ilmiah & Pola</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Rata-rata Skor TKA Total</span>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($avgOverallTka, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Gabungan Semua Domain</div>
        </div>
    </div>

    <!-- Tabel Peringkat Nilai TKA -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-base">Peringkat & Hasil Seleksi Jalur Nilai TKA</h3>
                <p class="text-xs text-slate-400 mt-0.5">Diurutkan otomatis dari Skor Akhir Terbobot tertinggi menuju terendah</p>
            </div>

            <form action="{{ route('spmb.tka.index') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium">
                    <option value="">-- Semua Status Seleksi --</option>
                    <option value="Lolos Seleksi" {{ request('status') === 'Lolos Seleksi' ? 'selected' : '' }}>Lolos Seleksi</option>
                    <option value="Daftar Ulang" {{ request('status') === 'Daftar Ulang' ? 'selected' : '' }}>Daftar Ulang</option>
                    <option value="Cadangan" {{ request('status') === 'Cadangan' ? 'selected' : '' }}>Cadangan</option>
                    <option value="Terdaftar" {{ request('status') === 'Terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No. TKA..." class="px-3 py-2 pl-8 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs w-48">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button type="submit" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold transition">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-amber-50/60 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold border-b border-amber-100 dark:border-slate-800 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 text-center">Rank</th>
                        <th class="py-3.5 px-4">No. Ujian TKA</th>
                        <th class="py-3.5 px-4">Peserta & Asal Sekolah</th>
                        <th class="py-3.5 px-4 text-center">Literasi</th>
                        <th class="py-3.5 px-4 text-center">Numerasi</th>
                        <th class="py-3.5 px-4 text-center">Sains</th>
                        <th class="py-3.5 px-4 text-center">Rata-rata TKA</th>
                        <th class="py-3.5 px-4 text-center">Rapor SMP</th>
                        <th class="py-3.5 px-4 text-center font-black text-amber-900 dark:text-amber-300">Skor Akhir (60:40)</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($tkaParticipants as $idx => $p)
                    <tr class="hover:bg-amber-50/30 dark:hover:bg-slate-800/40 transition {{ $p->tka_rank && $p->tka_rank <= $quotaTka ? 'bg-emerald-50/20' : '' }}">
                        <td class="py-3 px-4 text-center font-black">
                            @if($p->tka_rank === 1)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-amber-950 shadow-xs text-xs">🥇1</span>
                            @elseif($p->tka_rank === 2)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-300 text-slate-900 shadow-xs text-xs">🥈2</span>
                            @elseif($p->tka_rank === 3)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-600 text-white shadow-xs text-xs">🥉3</span>
                            @elseif($p->tka_rank)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg {{ $p->tka_rank <= $quotaTka ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $p->tka_rank }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 font-mono font-bold text-amber-700 dark:text-amber-400 whitespace-nowrap">
                            {{ $p->tka_exam_number ?? $p->registration_number }}
                            <div class="text-[10px] text-slate-400 font-normal">{{ $p->tka_exam_session ?? 'Lab CBT' }}</div>
                        </td>

                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $p->full_name }}</div>
                            <div class="text-[11px] text-slate-400">NISN: {{ $p->nisn }} &bull; {{ $p->school_origin ?? '-' }}</div>
                        </td>

                        <td class="py-3 px-4 text-center font-mono font-semibold">
                            {{ $p->tka_score_literacy !== null ? number_format((float) $p->tka_score_literacy, 2) : '-' }}
                        </td>

                        <td class="py-3 px-4 text-center font-mono font-semibold">
                            {{ $p->tka_score_numeracy !== null ? number_format((float) $p->tka_score_numeracy, 2) : '-' }}
                        </td>

                        <td class="py-3 px-4 text-center font-mono font-semibold">
                            {{ $p->tka_score_science !== null ? number_format((float) $p->tka_score_science, 2) : '-' }}
                        </td>

                        <td class="py-3 px-4 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400">
                            {{ $p->tka_score_total !== null ? number_format((float) $p->tka_score_total, 2) : '-' }}
                        </td>

                        <td class="py-3 px-4 text-center font-mono font-semibold text-slate-800 dark:text-slate-200">
                            {{ number_format((float) $p->average_score, 2) }}
                        </td>

                        <td class="py-3 px-4 text-center font-mono font-black text-amber-800 dark:text-amber-300 text-sm bg-amber-50/50 dark:bg-amber-950/20">
                            {{ $p->tka_weighted_score !== null ? number_format((float) $p->tka_weighted_score, 2) : '-' }}
                        </td>

                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $p->getStatusBadgeClass() }}">
                                {{ $p->status }}
                            </span>
                        </td>

                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button onclick="openTkaScoreModal('{{ $p->id }}', '{{ $p->full_name }}', '{{ $p->tka_score_literacy }}', '{{ $p->tka_score_numeracy }}', '{{ $p->tka_score_science }}', '{{ $p->tka_exam_session }}', '{{ $p->tka_status }}')" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-amber-950 font-black text-xs transition cursor-pointer shadow-2xs">
                                ✏️ Nilai TKA
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-12 text-slate-400">
                            Belum ada peserta pendaftar pada Jalur Tes Kemampuan Akademik (TKA).
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $tkaParticipants->links() }}
        </div>
    </div>
</div>

<!-- Modal Input / Edit Nilai TKA Peserta -->
<div id="modal-tka-score" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Input Skor Tes Kemampuan Akademik (TKA)</h3>
                <p id="tka-modal-student" class="text-xs text-amber-600 font-bold mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('modal-tka-score').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-tka-score" method="POST" class="space-y-4 mt-4 text-xs">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Skor Literasi *</label>
                    <input type="number" step="0.01" min="0" max="100" name="tka_score_literacy" id="input-lit" placeholder="0-100" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 font-bold text-sm text-center" required>
                    <span class="text-[10px] text-slate-400 mt-0.5 block text-center">Bhs Indo & Inggris</span>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Skor Numerasi *</label>
                    <input type="number" step="0.01" min="0" max="100" name="tka_score_numeracy" id="input-num" placeholder="0-100" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 font-bold text-sm text-center" required>
                    <span class="text-[10px] text-slate-400 mt-0.5 block text-center">Matematika Dasar</span>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Skor Sains / Logika *</label>
                    <input type="number" step="0.01" min="0" max="100" name="tka_score_science" id="input-sci" placeholder="0-100" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 font-bold text-sm text-center" required>
                    <span class="text-[10px] text-slate-400 mt-0.5 block text-center">Penalaran Ilmiah</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ruang / Sesi Ujian TKA</label>
                    <input type="text" name="tka_exam_session" id="input-session" placeholder="Sesi 1 (Lab CBT 1)" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran Ujian</label>
                    <select name="tka_status" id="input-tka-status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold">
                        <option value="Selesai Ujian">Selesai Ujian</option>
                        <option value="Hadir Ujian">Hadir Ujian</option>
                        <option value="Belum Ujian">Belum Ujian</option>
                        <option value="Tidak Hadir">Tidak Hadir (Gugur)</option>
                    </select>
                </div>
            </div>

            <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[11px] text-amber-900 dark:text-amber-200">
                💡 <strong>Kalkulasi Otomatis:</strong> Sistem akan langsung menghitung Rata-rata Skor TKA dan Skor Akhir Terbobot (60% Skor TKA + 40% Rapor SMP) serta memperbarui posisi perangkingan secara realtime.
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('modal-tka-score').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black shadow-md">Simpan & Hitung Skor</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTkaScoreModal(id, name, lit, num, sci, session, status) {
    document.getElementById('tka-modal-student').innerText = name;
    document.getElementById('input-lit').value = lit || '';
    document.getElementById('input-num').value = num || '';
    document.getElementById('input-sci').value = sci || '';
    document.getElementById('input-session').value = session || '';
    document.getElementById('input-tka-status').value = status || 'Selesai Ujian';
    document.getElementById('form-tka-score').action = '/spmb/' + id + '/tka-score';
    document.getElementById('modal-tka-score').classList.remove('hidden');
}
</script>
@endsection

