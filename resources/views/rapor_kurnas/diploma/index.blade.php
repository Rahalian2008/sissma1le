@extends('layouts.app')

@section('title', 'Ijazah & Transkrip Kelulusan - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>🎓</span>
                <span>Pengelolaan Ijazah & Transkrip Nilai Kelulusan</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola nomor ijazah nasional, nomor transkrip sekolah, tanggal kelulusan, dan cetak transkrip nilai resmi Kelas XII (Standar e-Rapor 2025 & Permendikdasmen 13/2025).
            </p>
        </div>

        <!-- Filter Kelas & Tombol Aksi -->
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('rapor_kurnas.diploma') }}">
                <select name="class_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-bold">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            Kelas {{ $c->name }} ({{ $c->grade }})
                        </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('rapor_kurnas.diploma.template', ['class_id' => $selectedClassId]) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center gap-1.5 transition">
                <span>📥 Unduh Format CSV</span>
            </a>

            <button type="button" onclick="document.getElementById('importDiplomaModal').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs flex items-center gap-1.5 transition cursor-pointer">
                <span>📤 Impor Data CSV</span>
            </button>
        </div>
    </div>

    <!-- Modal Impor Data CSV -->
    <div id="importDiplomaModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Impor Nomor Ijazah & Transkrip</h3>
                <button type="button" onclick="document.getElementById('importDiplomaModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form action="{{ route('rapor_kurnas.diploma.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Berkas CSV Terisi</label>
                    <input type="file" name="csv_file" accept=".csv,text/csv" required class="w-full text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-[10px] text-slate-400 mt-1">Gunakan template yang diunduh dari tombol "Unduh Format CSV".</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('importDiplomaModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-500 font-bold hover:bg-slate-100 dark:hover:bg-slate-800">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-xs">Unggah & Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Kelola Nomor Ijazah & Kelulusan -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
        <form action="{{ route('rapor_kurnas.diploma.store') }}" method="POST">
            @csrf

            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">
                        Daftar Peserta Didik Kelas {{ $selectedClass->name ?? '-' }} ({{ $students->count() }} Siswa)
                    </h2>
                    <p class="text-xs text-slate-400">
                        Isi Nomor Ijazah Nasional, Nomor Transkrip, Tanggal Kelulusan, dan Status Kelulusan.
                    </p>
                </div>

                @if($students->isNotEmpty())
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition">
                        💾 Simpan Perubahan Data
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3 w-10 text-center">No</th>
                            <th class="p-3">Nama Siswa / NISN</th>
                            <th class="p-3 w-56">Nomor Ijazah Nasional</th>
                            <th class="p-3 w-48">Nomor Transkrip Sekolah</th>
                            <th class="p-3 w-36">Tanggal Kelulusan</th>
                            <th class="p-3 w-28 text-center">Rata2 Ijazah</th>
                            <th class="p-3 w-32">Status Kelulusan</th>
                            <th class="p-3 text-center w-28">Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($students as $idx => $st)
                            @php $tr = $st->kurnasTranscript; @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3">
                                    <input type="hidden" name="transcripts[{{ $idx }}][student_id]" value="{{ $st->id }}">
                                    <span class="font-bold text-slate-800 dark:text-white block">{{ $st->name }}</span>
                                    <span class="text-[10px] text-slate-400">NIS: {{ $st->nis ?? '-' }} | NISN: {{ $st->nisn ?? '-' }}</span>
                                </td>
                                <td class="p-3">
                                    <input type="text" name="transcripts[{{ $idx }}][diploma_number]" value="{{ $tr?->diploma_number }}" placeholder="DN-02/SMA/KURNAS/2025/..." class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2.5 py-1 font-mono">
                                </td>
                                <td class="p-3">
                                    <input type="text" name="transcripts[{{ $idx }}][transcript_number]" value="{{ $tr?->transcript_number }}" placeholder="421.3/TR-..." class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2.5 py-1 font-mono">
                                </td>
                                <td class="p-3">
                                    <input type="date" name="transcripts[{{ $idx }}][graduation_date]" value="{{ $tr?->graduation_date ? $tr->graduation_date->format('Y-m-d') : date('Y-05-05') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2 py-1">
                                </td>
                                <td class="p-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100" name="transcripts[{{ $idx }}][average_score]" value="{{ $tr?->average_score }}" placeholder="85.5" class="w-20 text-center rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-1 font-bold">
                                </td>
                                <td class="p-3">
                                    <select name="transcripts[{{ $idx }}][status_graduation]" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2 py-1 font-bold {{ ($tr?->status_graduation ?? 'LULUS') === 'LULUS' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        <option value="LULUS" {{ ($tr?->status_graduation ?? 'LULUS') === 'LULUS' ? 'selected' : '' }}>LULUS</option>
                                        <option value="TIDAK LULUS" {{ ($tr?->status_graduation ?? '') === 'TIDAK LULUS' ? 'selected' : '' }}>TIDAK LULUS</option>
                                    </select>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('rapor_kurnas.print_diploma', $st) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-400 font-bold hover:bg-teal-100 text-[11px] inline-flex items-center gap-1 transition">
                                        <span>🖨️ Transkrip</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-400">
                                    Belum ada data siswa di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                        💾 Simpan Seluruh Perubahan Ijazah
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

