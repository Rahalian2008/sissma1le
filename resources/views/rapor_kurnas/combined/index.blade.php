@extends('layouts.app')

@section('title', 'Mata Pelajaran Gabungan IPA & IPS - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>🧬</span>
                <span>Mata Pelajaran Gabungan (IPA & IPS Fase E Kelas X)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pengelolaan mata pelajaran rumpun sains dan sosial sesuai Panduan Resmi e-Rapor SMA Versi 2025 (Halaman 44-46 & 60-61).
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $setting->combine_science_social ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                Status Cetak: {{ $setting->combine_science_social ? '✓ Gabungkan IPA/IPS Aktif' : 'Tampil Terpisah (Standar)' }}
            </span>
            <a href="{{ route('rapor_kurnas.settings') }}" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold transition">
                ⚙️ Ubah di Pengaturan
            </a>
        </div>
    </div>

    <!-- Official Guide Information Box -->
    <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 flex items-start gap-3.5">
        <span class="text-2xl mt-0.5">ℹ️</span>
        <div class="text-xs text-indigo-950 dark:text-indigo-200 space-y-1">
            <p class="font-bold text-sm text-indigo-900 dark:text-indigo-100">Aturan Rapor Kurikulum Merdeka Jenjang SMA (Permendikdasmen No. 13 Tahun 2025):</p>
            <p>1. Pada <strong>Fase E (Kelas X)</strong>, mata pelajaran IPA (Fisika, Kimia, Biologi) dan IPS (Sosiologi, Ekonomi, Geografi, Sejarah) dapat diajarkan secara terpisah oleh masing-masing guru pengampu, namun nilai akhir pada rapor resmi dapat digabungkan menjadi satu baris terpadu.</p>
            <p>2. Nilai gabungan dihitung otomatis dari nilai rata-rata mata pelajaran pembentuknya, dan deskripsi capaian kompetensi dirangkum dari capaian optimal dan peningkatan masing-masing cabang ilmu.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tabel Daftar Mapel Gabungan (2 Kolom) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Rumpun Mapel Gabungan</h2>
                    <p class="text-xs text-slate-400">Rumpun yang terdaftar akan diterapkan pada lembar rapor cetak Kelas X.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3 w-12 text-center">Urutan</th>
                            <th class="p-3">Nama Gabungan</th>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Mata Pelajaran Pembentuk (Sumber)</th>
                            <th class="p-3 text-center">Tingkat</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($combinedSubjects as $cs)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $cs->order_number }}</td>
                                <td class="p-3 font-bold text-slate-800 dark:text-white">
                                    {{ $cs->combined_name }}
                                </td>
                                <td class="p-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $cs->combined_code }}
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($cs->source_subjects as $sub)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                {{ $sub->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-3 text-center font-bold">
                                    Kelas {{ $cs->grade }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($cs->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('rapor_kurnas.combined_subjects.destroy', $cs) }}" method="POST" onsubmit="return confirm('Hapus pengaturan mapel gabungan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold text-[11px] cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    Belum ada data mata pelajaran gabungan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Tambah Mapel Gabungan (1 Kolom) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs h-fit space-y-4">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                <span>➕</span>
                <span>Tambah / Atur Rumpun Baru</span>
            </h2>

            <form action="{{ route('rapor_kurnas.combined_subjects.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                    <select name="grade" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white px-3 py-2 font-bold">
                        <option value="X" selected>Kelas X (Fase E)</option>
                        <option value="XI">Kelas XI (Fase F)</option>
                        <option value="XII">Kelas XII (Fase F)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Mapel Gabungan di Rapor</label>
                    <input type="text" name="combined_name" placeholder="Contoh: Ilmu Pengetahuan Alam (IPA)" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white px-3 py-2 font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Gabungan</label>
                    <input type="text" name="combined_code" placeholder="Contoh: IPA-X" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white px-3 py-2 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Mata Pelajaran Sumber (Minimal 2)</label>
                    <div class="max-h-48 overflow-y-auto p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 space-y-1.5 scrollbar-thin">
                        @foreach($subjects as $sub)
                            <label class="flex items-center gap-2 text-[11px] text-slate-700 dark:text-slate-300 hover:text-indigo-600 cursor-pointer">
                                <input type="checkbox" name="source_subject_ids[]" value="{{ $sub->id }}" class="rounded text-indigo-600 w-3.5 h-3.5">
                                <span>{{ $sub->name }} ({{ $sub->code }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Urut Tampil</label>
                    <input type="number" name="order_number" value="1" min="1" max="99" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white px-3 py-2 font-bold">
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-white block">Status Aktif</span>
                        <span class="text-[10px] text-slate-400">Aktifkan penggabungan pada rapor</span>
                    </div>
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-indigo-600 w-4 h-4">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                    Simpan Mapel Gabungan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

