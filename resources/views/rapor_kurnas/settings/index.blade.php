@extends('layouts.app')

@section('title', 'Pengaturan & Referensi - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>⚙️</span>
                <span>Pengaturan & Referensi Rapor Kurnas</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Konfigurasi periode semester aktif, tanggal pembagian rapor, pejabat penandatangan, status buka/tutup nilai, dan urutan kelompok mata pelajaran.
            </p>
        </div>

        <form action="{{ route('rapor_kurnas.toggle_lock') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-xl font-bold text-xs shadow-md transition cursor-pointer flex items-center gap-2 {{ $setting->is_locked ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'bg-rose-600 hover:bg-rose-500 text-white' }}">
                <span>{{ $setting->is_locked ? '🔓 Buka Kunci Input Nilai' : '🔒 Kunci / Tutup Input Nilai' }}</span>
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Pengaturan Periode & Tanda Tangan (1 Kolom) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs h-fit space-y-4">
            <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                <span>📅</span>
                <span>Periode & Tanda Tangan</span>
            </h2>

            <form action="{{ route('rapor_kurnas.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Ajaran Aktif</label>
                    <select name="academic_year_id" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $setting->academic_year_id == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Semester</label>
                    <select name="semester" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                        <option value="1" {{ $setting->semester == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                        <option value="2" {{ $setting->semester == '2' ? 'selected' : '' }}>2 (Genap)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembagian Rapor</label>
                    <input type="date" name="report_date" value="{{ $setting->report_date ? $setting->report_date->toDateString() : date('Y-m-d') }}" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Penerbitan Dokumen</label>
                    <input type="text" name="report_place" value="{{ $setting->report_place ?? 'Lengkong' }}" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kepala Sekolah</label>
                    <input type="text" name="headmaster_name" value="{{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIP Kepala Sekolah</label>
                    <input type="text" name="headmaster_nip" value="{{ $setting->headmaster_nip ?? '196805141994121001' }}" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-mono">
                </div>

                <!-- Unggah Tanda Tangan & Stempel Digital -->
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 space-y-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanda Tangan Digital Kepala Sekolah (PNG Transparan)</label>
                        @if($setting->headmaster_signature)
                            <div class="mb-2 flex items-center gap-2">
                                <img src="{{ asset('storage/'.$setting->headmaster_signature) }}" alt="TTD Kepsek" class="h-10 border border-slate-200 rounded p-1 bg-white">
                                <span class="text-[10px] text-emerald-600 font-semibold">✓ TTD Tersimpan</span>
                            </div>
                        @endif
                        <input type="file" name="headmaster_signature" accept="image/png,image/jpeg" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Stempel Resmi Sekolah (PNG Transparan)</label>
                        @if($setting->school_stamp)
                            <div class="mb-2 flex items-center gap-2">
                                <img src="{{ asset('storage/'.$setting->school_stamp) }}" alt="Stempel Sekolah" class="h-10 border border-slate-200 rounded p-1 bg-white">
                                <span class="text-[10px] text-emerald-600 font-semibold">✓ Stempel Tersimpan</span>
                            </div>
                        @endif
                        <input type="file" name="school_stamp" accept="image/png,image/jpeg" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <!-- Toggles Konfigurasi Rapor -->
                <div class="space-y-2">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Tampilkan TTD & Stempel Digital</span>
                            <span class="text-[10px] text-slate-400">Tampilkan tanda tangan & stempel pada cetakan rapor dinas</span>
                        </div>
                        <input type="checkbox" name="show_signature_image" value="1" {{ $setting->show_signature_image ? 'checked' : '' }} class="rounded text-indigo-600 w-4 h-4">
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Gabungkan IPA & IPS Kelas X (Fase E)</span>
                            <span class="text-[10px] text-slate-400">Fisika/Kimia/Bio jadi IPA, Sos/Eko/Geo/Sej jadi IPS</span>
                        </div>
                        <input type="checkbox" name="combine_science_social" value="1" {{ $setting->combine_science_social ? 'checked' : '' }} class="rounded text-indigo-600 w-4 h-4">
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Publikasikan ke Siswa & Wali Murid</span>
                            <span class="text-[10px] text-slate-400">Siswa dapat melihat & download rapor di portal mandiri</span>
                        </div>
                        <input type="checkbox" name="is_published_to_students" value="1" {{ $setting->is_published_to_students ? 'checked' : '' }} class="rounded text-indigo-600 w-4 h-4">
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Kunci Input Nilai Rapor</span>
                            <span class="text-[10px] text-slate-400">Guru tidak dapat mengubah nilai jika dicentang</span>
                        </div>
                        <input type="checkbox" name="is_locked" value="1" {{ $setting->is_locked ? 'checked' : '' }} class="rounded text-indigo-600 w-4 h-4">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                    Simpan Pengaturan Rapor
                </button>
            </form>
        </div>

        <!-- Tabel Mapping Kelompok Mata Pelajaran (2 Kolom) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span>📚</span>
                        <span>Struktur & Urutan Kelompok Mata Pelajaran Rapor</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Atur pengelompokan (A: Umum, B: Pilihan, C: Muatan Lokal) dan nomor urut tampil pada lembar rapor resmi.
                    </p>
                </div>
            </div>

            <form action="{{ route('rapor_kurnas.settings.update_groups') }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="p-3 w-12 text-center">Urutan</th>
                                <th class="p-3">Mata Pelajaran Master</th>
                                <th class="p-3 w-36">Kelompok</th>
                                <th class="p-3 w-56">Nama Tampil di Rapor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($subjectGroups as $idx => $sg)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                    <td class="p-3 text-center">
                                        <input type="hidden" name="mappings[{{ $idx }}][id]" value="{{ $sg->id }}">
                                        <input type="number" name="mappings[{{ $idx }}][order_number]" value="{{ $sg->order_number }}" min="1" max="99" class="w-12 text-center rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-1 font-bold">
                                    </td>
                                    <td class="p-3 font-bold text-slate-800 dark:text-white">
                                        {{ $sg->subject->name ?? '-' }}
                                    </td>
                                    <td class="p-3">
                                        <select name="mappings[{{ $idx }}][group_type]" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2 py-1 font-semibold">
                                            <option value="A" {{ $sg->group_type === 'A' ? 'selected' : '' }}>A (Umum)</option>
                                            <option value="B" {{ $sg->group_type === 'B' ? 'selected' : '' }}>B (Pilihan)</option>
                                            <option value="C" {{ $sg->group_type === 'C' ? 'selected' : '' }}>C (Mulok)</option>
                                        </select>
                                    </td>
                                    <td class="p-3">
                                        <input type="text" name="mappings[{{ $idx }}][display_name]" value="{{ $sg->display_name ?? $sg->subject->name }}" placeholder="Nama cetak di rapor..." class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-2.5 py-1">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                        Simpan Perubahan Struktur Mapel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

