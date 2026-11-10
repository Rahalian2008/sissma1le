@extends('layouts.app')

@section('title', 'Tujuan Pembelajaran (TP) - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>🎯</span>
                <span>Tujuan Pembelajaran (TP)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola indikator Tujuan Pembelajaran per mata pelajaran dan fase untuk dasar penilaian capaian kompetensi e-Rapor 2025.
            </p>
        </div>

        <button type="button" onclick="document.getElementById('modalAddTp').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition cursor-pointer shrink-0">
            <span>+ Tambah TP Baru</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('rapor_kurnas.learning_goals') }}" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Pelajaran</label>
                <select name="subject_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                    @foreach($subjects as $subj)
                        <option value="{{ $subj->id }}" {{ $selectedSubjectId == $subj->id ? 'selected' : '' }}>
                            {{ $subj->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Fase Kurikulum</label>
                <select name="fase" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                    <option value="E" {{ $selectedFase == 'E' ? 'selected' : '' }}>Fase E (Kelas X)</option>
                    <option value="F" {{ $selectedFase == 'F' ? 'selected' : '' }}>Fase F (Kelas XI & XII)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                <select name="grade" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                    <option value="X" {{ $selectedGrade == 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ $selectedGrade == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ $selectedGrade == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Semester</label>
                <select name="semester" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                    <option value="1" {{ $selectedSemester == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                    <option value="2" {{ $selectedSemester == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Alert Pedoman Kurnas 2025 -->
    <div class="bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl p-4 text-xs text-indigo-900 dark:text-indigo-300 leading-relaxed flex items-start gap-3">
        <span class="text-lg">💡</span>
        <div>
            <strong class="font-bold">Ketentuan Standar e-Rapor SMA 2025:</strong>
            <p class="mt-0.5 text-indigo-800 dark:text-indigo-400">
                Deskripsi Tujuan Pembelajaran (TP) harus diawali dengan <strong>huruf kecil</strong> dan <strong>tanpa mencantumkan frasa</strong> <em>"peserta didik dapat..."</em> atau <em>"siswa mampu..."</em>. Contoh: <em>"memahami hukum Newton dalam gerak benda"</em>.
            </p>
        </div>
    </div>

    <!-- Tabel Daftar TP -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">
                Daftar Tujuan Pembelajaran Aktif ({{ count($learningGoals) }})
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <th class="p-3.5 w-16 text-center">Kode</th>
                        <th class="p-3.5 w-44">Mata Pelajaran</th>
                        <th class="p-3.5 w-24">Fase / Kelas</th>
                        <th class="p-3.5">Deskripsi Tujuan Pembelajaran (TP)</th>
                        <th class="p-3.5 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($learningGoals as $goal)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="p-3.5 text-center font-black text-indigo-600 dark:text-indigo-400">
                                {{ $goal->code }}
                            </td>
                            <td class="p-3.5 font-semibold text-slate-800 dark:text-white">
                                {{ $goal->subject->name ?? '-' }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    Fase {{ $goal->fase }} ({{ $goal->grade }})
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-700 dark:text-slate-300 leading-relaxed font-mono text-[11.5px]">
                                {{ $goal->description }}
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick="openEditModal({{ $goal->id }}, '{{ addslashes($goal->code) }}', '{{ addslashes($goal->description) }}')" class="px-2.5 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100 font-semibold text-[11px] transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('rapor_kurnas.learning_goals.destroy', $goal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus TP ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 font-semibold text-[11px] transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                <span class="text-3xl block mb-2">📂</span>
                                Belum ada Tujuan Pembelajaran yang diinput untuk filter ini. Silakan klik tombol <strong>+ Tambah TP Baru</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah TP -->
<div id="modalAddTp" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl text-slate-800 dark:text-white space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold flex items-center gap-2">
                <span>🎯</span>
                <span>Tambah Tujuan Pembelajaran (TP)</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalAddTp').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form action="{{ route('rapor_kurnas.learning_goals.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold mb-1">Mata Pelajaran</label>
                <select name="subject_id" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
                    @foreach($subjects as $subj)
                        <option value="{{ $subj->id }}" {{ $selectedSubjectId == $subj->id ? 'selected' : '' }}>
                            {{ $subj->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold mb-1">Fase</label>
                    <select name="fase" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
                        <option value="E" {{ $selectedFase == 'E' ? 'selected' : '' }}>Fase E</option>
                        <option value="F" {{ $selectedFase == 'F' ? 'selected' : '' }}>Fase F</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Kelas</label>
                    <select name="grade" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
                        <option value="X" {{ $selectedGrade == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ $selectedGrade == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ $selectedGrade == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Semester</label>
                    <select name="semester" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
                        <option value="1" {{ $selectedSemester == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                        <option value="2" {{ $selectedSemester == '2' ? 'selected' : '' }}>2 (Genap)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold mb-1">Kode TP (misal: TP 1, TP 2)</label>
                <input type="text" name="code" value="TP {{ count($learningGoals) + 1 }}" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
            </div>

            <div>
                <label class="block font-bold mb-1">Deskripsi Bunyi TP (Huruf kecil awal)</label>
                <textarea name="description" rows="3" required placeholder="contoh: menganalisis struktur dan kaidah kebahasaan teks eksposisi" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2 font-mono"></textarea>
                <p class="text-[10px] text-slate-400 mt-1">Jangan gunakan kata pembuka seperti 'peserta didik dapat/mampu'. Awali dengan kata kerja operasional.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modalAddTp').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 font-bold text-xs text-slate-700 dark:text-slate-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-xs text-white shadow-md">
                    Simpan TP
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit TP -->
<div id="modalEditTp" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl text-slate-800 dark:text-white space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold flex items-center gap-2">
                <span>✏️</span>
                <span>Edit Tujuan Pembelajaran</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalEditTp').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form id="formEditTp" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold mb-1">Kode TP</label>
                <input type="text" id="editCode" name="code" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
            </div>

            <div>
                <label class="block font-bold mb-1">Deskripsi Bunyi TP</label>
                <textarea id="editDescription" name="description" rows="3" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2 font-mono"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modalEditTp').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-xs text-white shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, code, description) {
    document.getElementById('formEditTp').action = `/rapor-kurnas/learning-goals/${id}`;
    document.getElementById('editCode').value = code;
    document.getElementById('editDescription').value = description;
    document.getElementById('modalEditTp').classList.remove('hidden');
}
</script>
@endsection

